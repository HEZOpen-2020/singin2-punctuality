#include "valcomp.h"
///// BEGIN Fake Sometimes TLE Library RotateTreap /////
namespace F_stl {
    #ifndef _FSTL_LIB_ROTATE_TREAP
    #define _FSTL_LIB_ROTATE_TREAP

    //旋转式Treap节点（请不要自行创建）
    template<class T>
    struct RotateTreapNode {
        //初值
        T __init;

        //父子关系指针
        RotateTreapNode *fa,*ch[2];

        //数值
        T val;
        //权值
        int rank;
        //频数与大小 
        int freq,sz;
        //构造默认节点
        RotateTreapNode() {
            freq=sz=0;
            val=__init;
            ch[0]=ch[1]=fa=NULL;
            rank=rand();
        }
        //构造带数值节点
        RotateTreapNode(T _val) {
            freq=sz=1;
            val=_val;
            ch[0]=ch[1]=fa=NULL;
            rank=rand();
        }
        //确定位置
        int identify() {
            if(!fa) return -1;
            if(fa->ch[0]==this) return 0;
            return 1; 
        } 
    };

    //通用旋转式Treap平衡树类（指针，基础迭代器。调用传入class T的小于号）
    //非调试操作均为非递归结构 
    //兼容树套树结构
    template<class T,class CT=ValComp_less<T> >
    struct RotateTreap {
    private:
        //定义使用的节点
        typedef RotateTreapNode<T> Node;

        //无穷大
        static const int INF=0x7fffffff; 

        //比较器
        CT comp;

        //初值
        T __init;

        //树的超级根
        Node *rt;

        //清空情景下使用的栈
        stack<Node*> clear_s; 

    public:
        //参数：删除操作>删除全部数
        static const bool ERASE_ALL=true;

        //参数：删除操作>删除一个数
        static const bool ERASE_ONE=false;

    private:
        //上传元素个数信息
        inline void push_up(Node *p) {
            p->sz = (p->ch[0]?p->ch[0]->sz:0)+(p->ch[1]?p->ch[1]->sz:0)+p->freq;
        }

        //判断对于父节点的位置，不存在返回-1
        inline int identify(Node *p) {
            if(p->fa==NULL) return -1;
            if(p->fa->ch[0]==p) return 0;
            return 1;
        }

        //双向建立u到f的连接
        inline void connect(Node *u,Node *f,int id) {
            f->ch[id]=u;
            u->fa=f;
        }

        //旋转节点x到其父节点位置（需要x的祖父辅助） 
        inline void rotate(Node *x) {
            Node *y=x->fa;
            Node *R=y->fa;
            int Rs=identify(y);
            int ys=identify(x);
            Node *B=x->ch[!ys];
            if(B) connect(B,y,ys);
            else y->ch[ys]=NULL;
            connect(y,x,!ys);
            connect(x,R,Rs);
            push_up(y);
            push_up(x);
        }

        //插入值
        inline Node *__insert(T val) {
            Node *curr=rt->ch[1];
            if(!curr) {
                Node *ret=new Node(val);
                ret->fa=rt;rt->ch[1]=ret;
                return (ret);
            }
            while(1) {
                curr->sz++;
                if(!comp(curr->val,val) && !comp(val,curr->val)) {
                    curr->freq++;
                    return curr;
                }
                Node *nxt=curr;
                int nxtid=0;
                if(comp(val,curr->val)) nxtid=0;
                else nxtid=1;
                nxt=curr->ch[nxtid];
                if(!nxt) {
                    nxt=new Node(val);
                    curr->ch[nxtid]=nxt;nxt->fa=curr;
                    return (nxt);
                } 
                curr=nxt;
            }
            return (NULL);
        } 

    public:
        //迭代器（使用方法同STL __RbTree_iterator）
        //支持操作：取位置 *it，自加 it++，自减 it--，比较 it==other，判断有效性 it.isNull() 
        struct iterator {
        public:
            Node *it;
            int idx;
            iterator() {it=NULL;}
            iterator(Node *_it) {it=_it;idx=1;}
            iterator(Node *_it,int _idx) {it=_it;idx=_idx;}

            //判定是否NULL 
            bool isNull() const {return !(it);}

            //获取元素出现次数
            int freq() {
                if(isNull()) return 0;
                return it->freq;
            } 

            //下一个本质不同的指针。不存在返回NULL
            iterator iter_next() {
                Node *it2=it;
                if(it2->ch[1]) {
                    Node *tmp=it2->ch[1];
                    while(tmp->ch[0]) {
                        tmp=tmp->ch[0];
                    }
                    return tmp;
                } //在中序遍历上进一步到右侧的最左节点
                while(it2->fa && it2->identify()==1) {it2=it2->fa;} //回溯至可进行下一步遍历
                if(!it2->fa) return iterator(NULL); //退到超级根，无解。
                return iterator(it2->fa); //下一步
            }

            //上一个本质不同的指针。不存在返回NULL
            iterator iter_prev() {
                Node *it2=it;
                if(it2->ch[0]) {
                    Node *tmp=it2->ch[0];
                    while(tmp->ch[1]) {
                        tmp=tmp->ch[1];
                    }
                    return tmp;
                } //在中序遍历上进一步到左侧的最右节点
                while(it2->fa && it2->identify()==0) {it2=it2->fa;} //推进至可进行上一步遍历
                if(!it2->fa) return iterator(NULL); //退到超级根，无解。
                if(it2->fa->fa) return iterator(it2->fa); //上一步
                return iterator(NULL);
            }

            //取值（返回只读值） 
            inline const T &operator*() const {
                return it->val;
            }

            //前自加 
            inline iterator &operator++() {
                if(idx < (it->freq)) {idx++;return *this;}
                (*this) = (this->iter_next());
                idx=1;
                return *this;
            }

            //后自加
            inline iterator operator++(signed __suf) {
                iterator ret=(*this);
                ++(*this);
                return ret;
            } 

            //前自减 
            inline iterator &operator--() {
                if(idx > 1) {idx--;return *this;}
                (*this) = (this->iter_prev());
                if(it) idx=it->freq;
                return *this;
            }

            //后自减
            inline iterator operator--(signed __suf) {
                iterator ret=(*this);
                --(*this);
                return ret;
            }

            //相等
            bool operator==(const iterator &other) const {
                if(it==NULL && it==other.it) return true;
                return (it==other.it) && (idx==other.idx);
            } 

            //不相等
            bool operator!=(const iterator &other) const {
                return !operator==(other);
            }

            //不可用
            bool operator!() const {
                return isNull();
            }

            //指针操作
            const T *operator->() const {
                return &(it->val);
            }
        }; 

        //正向开始（指向第一个元素，没有则NULL）
        inline iterator begin() {
            Node *curr=rt->ch[1];
            if(!curr) return iterator(NULL);
            while(curr->ch[0]) curr=curr->ch[0];
            return iterator(curr,1);
        }

        //正向结束（指向最后一个元素后的位置，实则是NULL）
        inline iterator end() {
            return iterator(NULL,0);
        } 

        //反向开始（指向最后一个元素，没有则NULL）
        //注意返回的仍然是iterator（此时应使用--符号迭代）。Treap没有reverse_iterator。 
        inline iterator rbegin() {
            Node *curr=rt->ch[1];
            if(!curr) return iterator(NULL);
            while(curr->ch[1]) curr=curr->ch[1];
            return iterator(curr,curr->freq);
        } 

        //反向结束（指向第一个元素前的位置，实则是NULL）
        inline iterator rend() {
            return iterator(NULL,0);
        } 

        //清空树（放心食用） [3n]
        inline void clear() {
            if(!rt->ch[1]) return;
            clear_s.push(rt->ch[1]);
            while(!clear_s.empty()) {
                Node *curr=clear_s.top();
                if(curr->ch[0]) {
                    clear_s.push(curr->ch[0]);
                    curr->ch[0]=NULL;
                }
                else if(curr->ch[1]) {
                    clear_s.push(curr->ch[1]);
                    curr->ch[1]=NULL;
                }
                else {
                    delete curr;
                    clear_s.pop();
                }
            }
            rt->ch[1]=NULL;
        }

        //取大小
        inline int size() {
            if(!rt->ch[1]) return 0;
            return rt->ch[1]->sz;
        }

        //构造空Treap [2]
        RotateTreap() {
            rt=new Node();
            rt->rank=-INF;
        }

        //查找数值val，返回第一个迭代器 [3log2(n)]
        inline iterator find(T val) {
            Node *curr=rt->ch[1];
            if(!curr) return iterator(NULL);
            while(1) {
                if(!comp(curr->val,val) && !comp(val,curr->val)) {
                    return iterator(curr);
                }
                if(val < curr->val) curr=curr->ch[0];
                else curr=curr->ch[1];
                if(!curr) return iterator(NULL);
            }
        } 

        //插入数值，返回插入处的迭代器 [5log2(n)]
        //操作不会旋转树，不维护堆性质。一般情况不要直接使用
        //适用于需要高效率即插即删的情况
        //插入数已经存在时，增加原节点的频数
        //理论上不会返回NULL（除非你的编译命令是cxk） 
        inline iterator insert_t(T val) {
            return iterator(__insert(val));
        }

        //插入数值，返回插入处的迭代器 [8log2(n)]
        //插入完成后会旋转维护堆性质
        //插入数已经存在时，增加原节点的频数
        //理论上不会返回NULL（除非你的编译命令是cxk）
        inline iterator insert(T val) {
            Node *ret = __insert(val);
            while((ret->rank) < (ret->fa->rank)) {
                rotate(ret);
            }
            return iterator(ret);
        }

        //删除迭代器处的数值 [1 / 6log2(n)]
        //注意，如果删除后该节点频数为0，将移除节点，指针失效。
        //参数tp：默认false。
        //  RotateTreap::ERASE_ONE==false，频数自减，变为0则移除 
        //  RotateTreap::ERASE_ALL==true，直接移除节点
        inline void erase(iterator it,bool tp=ERASE_ONE) {
            Node *s = it.it;
            s->sz--;
            s->freq--;
            if(tp) s->freq=0;
            Node *dd=s->fa;
            if(s->freq==0) {
                while(1) {
                    int nxtid=0;
                    if(!s->ch[0]) nxtid=1;
                    else if(s->ch[1] && (s->ch[1]->rank) < (s->ch[0]->rank)) {
                        nxtid=1;
                    }
                    if(!s->ch[nxtid]) {
                        dd=s->fa;
                        dd->ch[identify(s)]=NULL;
                        delete s;
                        break;
                    }
                    else rotate(s->ch[nxtid]);
                }
            }
            while(dd) {
                push_up(dd);
                dd=dd->fa;
            }
        }

        //获取元素的排名（开始于1）。排名定义为：小于当前数的数值量
        inline int rk(T val) {
            int ret=0;
            Node *curr=rt->ch[1];
            if(!curr) return 1;
            while(1) {
                if(comp(curr->val,val)) {
                    ret+=curr->freq;
                    if(curr->ch[0]) ret+=curr->ch[0]->sz;
                    curr=curr->ch[1];
                }
                else {
                    curr=curr->ch[0];
                }
                if(!curr) return ret+1;
            }
        }

        //获取排名为urk的元素（开始于1）。越界则返回NULL 
        inline iterator atrk(int urk) {
            Node *curr=rt->ch[1];
            if(!curr) return iterator(NULL);
            while(1) {
                int l= (curr->sz) - (curr->ch[1]?(curr->ch[1]->sz):0);
                if((urk>(curr->ch[0]?curr->ch[0]->sz:0)) && urk<=l) break;
                if(urk<l) curr=curr->ch[0];
                else {
                    urk-=l;
                    curr=curr->ch[1];
                }
                if(!curr) return iterator(NULL);
            }
            return iterator(curr);
        }

        //查找小于x且最大的元素
        inline iterator prev_value(T val) {
            iterator tmp=insert_t(val); //拟插入值
            iterator ret=tmp.iter_prev(); //找到前一项
            erase(tmp,ERASE_ONE); //撤销插入值
            return ret;
        }

        //查找大于x且最小的元素
        inline iterator next_value(T val) {
            iterator tmp=insert_t(val); //拟插入值
            iterator ret=tmp.iter_next(); //找到后一项
            erase(tmp,ERASE_ONE); //撤销插入值
            return ret;
        }

        //查找upper_bound，返回指针（定义同STL upper_bound） 
        inline iterator upper_bound(T val) {
            return next_value(val);
        } 

        //查找lower_bound，返回指针（定义同STL lower_bound） 
        inline iterator lower_bound(T val) {
            iterator ret=find(val);
            if(!ret.isNull()) return ret;
            return next_value(val);
        } 

    private:
        //输出序列-递归函数
        int dump_idx;
        void __print(Node *n) {
            if(n->ch[0]) __print(n->ch[0]);
            dump_idx++;
            cout<<dump_idx<<"\t"<<(n->val)<<endl;
            if(n->ch[1]) __print(n->ch[1]);
        } 

    public:
        //输出序列
        inline void print() {
            if(!rt->ch[1]) {
                cout<<"Tree is empty."<<endl;
                return;
            }
            cout<<"Index\tValue"<<endl;
            dump_idx=0;
            __print(rt->ch[1]);
        } 
    };

    #endif
};
///// END Fake Sometimes TLE Library RotateTreap /////
